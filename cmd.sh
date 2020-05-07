#!/bin/bash
#!/usr/local/php7/bin/php

basepath=$(pwd)
serverfile="${basepath}/bin/hyperf.php"

app_name="pmir2"

echo "Folder:${serverfile}"
cd $basepath

# 重置文件缓存
init(){
  rm -rf runtime/container
  echo "Runtime cleared"
  return $!
}

# 停止服务
stop(){

  #ps aux |egrep "${app_name}" | cut -c 9-15 | xargs kill -9
  #sleep 1
 # echo "Stop!"
  #return $!

  # 判断主进程如果存在
  if [ -f "runtime/hyperf.pid" ];then
    cat runtime/hyperf.pid | awk '{print $1}' | xargs kill -9 && rm -rf runtime/hyperf.pid
  fi

  # 判断是否有残留进程，通知退出
#  local num=`count`
 # while [ $num -gt 0 ]; do
  #  echo "The worker num:${num}"
   # ps -ef | grep "${serverfile}" | grep -v "grep"| awk '{print $2}'| xargs kill -9
    #num=`count`
   # sleep 1
 # done

  ps aux |egrep "${app_name}" | cut -c 9-15 | xargs kill -9
  sleep 1

  echo "Stop!"
  return $!
}

# 进程数
count()
{
  echo `ps aux |grep "${app_name}" | grep -v "grep"| wc -l`
}

#查看状态
status(){
  local num=`count`
  if [ $num -gt 0 ];then
    if [ -f "runtime/hyperf.pid" ];then
      local pid=" pid:`cat runtime/hyperf.pid | awk '{print $1}'`"
    fi
    echo "Running!${pid} worker num:${num}"
  else
    echo "Close!"
  fi
  return $!
}

# 启动服务
start()
{
  local num=`count`
  if [ $num -gt 0 ];then
    status
    return $!
  fi
  init
  exec php "${serverfile}" start
  echo "Start!"
  return $!
}

# 平滑重启
reload()
{
  exec php "${serverfile}" reload
}

# 帮助文档
help()
{
    cat <<- EOF
    Usage:
        help [options] [<command_name>]Options:
    Options:
        stop      Stop swoole server
        start     Start swoole server
        restart   Restart swoole server
        status    Status swoole server check
        init      init proxy runtime created
        help      Help document
EOF
    return $!
}

case $1 in
  'stop')
    stop
  ;;
  'start')
    start
  ;;
  'restart')
    stop
    start
  ;;
  'reload')
    reload
  ;;
 'status')
    status
  ;;
 'init')
    init
  ;;
  *)
    help
  ;;
esac

exit 0
