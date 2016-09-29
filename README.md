# Necromancer

**This is a work in progress and is not ready for use.**

Necromancer is the easiest way to monitor a process is restarted once it is over.

You can try use necromancer running:

```shell
bin/necromancer execute your-command-here
```

or get more info running:

```shell
╰─$ bin/necromancer execute --help
Usage:
  execute <process> [<time>] [<times>]

Arguments:
  process               Who do you want to supervise?
  time                  How long do you want to wait to restart [default: 10]
  times                 How many times you want the process to restart, 
zero for infinite times [default: 10]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more 
  verbose output and 3 for debug
```
