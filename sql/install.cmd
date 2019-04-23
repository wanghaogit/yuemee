@ECHO OFF
REM 分尔多系统数据库安装脚本
REM 需要 C:\WAMP\gnu 安装 cygwin
REM 需要 C:\WAMP\mysql\my.ini

:START
MODE CON COLS=120 LINES=38
COLOR 1F
CLS
C:\WAMP\gnu\bin\bash.exe install_win.sh
