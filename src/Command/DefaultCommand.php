<?php
/*
 * This file is part of the Necromancer package.
 *
 * Copyright (c) Daniel González
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Daniel González <daniel@desarrolla2.com>
 */

namespace Necromancer\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * DefaultCommand
 */
class DefaultCommand extends Command
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var string
     */
    private $processName;

    /**
     * @var int
     */
    private $time;

    /**
     * @var int
     */
    private $signal = 0;

    protected function configure()
    {
        declare(ticks = 1);
        pcntl_signal(SIGINT, [$this, 'handleSignal']);

        $this->setName('execute')
            ->addArgument(
                'process',
                InputArgument::REQUIRED,
                'Who do you want to supervise?'
            )
            ->addArgument(
                'cwd',
                InputArgument::OPTIONAL,
                'The working directory or null to use the working dir of the current process',
                null
            )
            ->addArgument(
                'time',
                InputArgument::OPTIONAL,
                'How long do you want to wait to restart',
                10
            )
            ->addArgument(
                'times',
                InputArgument::OPTIONAL,
                'How many times you want the process to restart, zero for infinite times',
                10
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->processName = $input->getArgument('process');
        $cwd = $input->getArgument('cwd');
        $this->time = $input->getArgument('time');
        $times = $input->getArgument('times');
        $step = 0;
        while (($step <= $times | $times == 0) && $this->signal == 0) {
            $output->writeln(sprintf('Starting execution of `%s`', $this->processName));
            $this->process = new Process($this->processName, $cwd);
            $this->process->setTimeout(null);
            $this->process->setIdleTimeout(null);
            $this->process->run(
                function ($type, $buffer) {
                    if (Process::ERR === $type) {
                        echo 'ERR > '.$buffer;
                    } else {
                        echo $buffer;
                    }
                }
            );
            $this->process->stop();
            $output->writeln(sprintf('`%s` is terminated', $this->processName));
            $this->wait($this->time);
            $step++;
        }
    }

    /**
     * @param $seconds
     */
    private function wait($seconds)
    {
        $message = '';
        while ($seconds--) {
            $this->output->write(str_repeat("\x08", strlen($message)));
            $message = sprintf('Waiting for %d seconds', $seconds);
            $this->output->write($message);
            sleep(1);
        }

        $this->output->writeln(PHP_EOL.'Done.');
    }

    public function handleSignal($signal)
    {
        if ($signal == 2) {
            $signal == SIGKILL;
        }
        $this->signal = $signal;
        $this->output->writeln(PHP_EOL.sprintf('Sending signal %d to process.', $signal));
        $this->process->stop($this->time, $signal);
    }
}