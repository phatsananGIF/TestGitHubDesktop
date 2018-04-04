#!/bin/sh

#echo "$@"


if [ ! "$@" == ""  ] ; then

db=$(cat /var/www/html/ima2/.env|grep 'DB_DATABASE'|cut -d= -f2)
username=$(cat /var/www/html/ima2/.env|grep 'DB_USERNAME'|cut -d= -f2)
password=$(cat /var/www/html/ima2/.env|grep 'DB_PASSWORD'|cut -d= -f2)

#echo "$db $username $password" >> /tmp/xxx

echo -n "$@" >> /var/log/mytest.log


msg_len="$(echo "$@"|wc -L)"

##save in database 
dt=$(echo "$@"|awk '{print $1}')
dt="$(date -d "$dt" "+%Y-%m-%d %H:%M:%S")"
ip="$(echo "$@"|awk '{print $2}')"
service="$(echo "$@"|awk '{print $3}')"

service_len="$(echo "$dt $ip $service 1234567"|wc -L)"

[ $service_len -ge 1 ] && {
 msg="$(echo "$@"|cut -c${service_len}-${msg_len}|tr -d "'")"
}


##mysql script 
#ignore ipaddr , service , message
ignore=0

#ipaddr 
for ig in $(grep -v '#' /var/www/html/ima2/public/rsyslog/ignore/ipaddr 2>/dev/null) 
do
 [ -n "$(echo "$ip"|grep "^${ig}$")" ] && { 
  ignore=1   
  break;
 }
done

if [ $ignore -eq 0 ] ; then
#service
for ig in $(grep -v '#' /var/www/html/ima2/public/rsyslog/ignore/service 2>/dev/null)
do

[ -n "$(echo "$service"|grep "^${ig}")" ] && { 
 ignore=1;
 break;
}

done
fi

if [ $ignore -eq 0 ] ; then

  [ -n "$(echo "$msg"|grep "segfault")" ] && { 
    [ ! -f /tmp/segfault/$ip ] && echo "$ip" > /tmp/segfault/$ip
  } 

#message
for ig in $(grep -v '#' /var/www/html/ima2/public/rsyslog/ignore/message 2>/dev/null)
do
  [ -n "$(echo "$msg"|grep "${ig}")" ] && {
    ignore=1;
    break;
  }
done

fi 

if [ $ignore -eq 0 ] ; then

 add="$(date +'%Y-%m-%d %H:%M:%S')"
 sql="INSERT INTO ${db}.im_rsyslog (log_datetime, log_ipaddr, log_service, log_message,adddatetime) VALUES ( '$dt', '$ip', '$service', '$msg', '$add');"

 mysql -u${username} -p${password} ${db} -e "$sql"

fi

#if [ -n "$(echo \"$msg\"|grep 'attempt')" ] ; then


##check passwrd attempt
##get cus_code

#sql="SELECT cus_code FROM ${db}.im_cus_radius WHERE rad_ipaddress = '$ip' AND is_deleted = '0'";
#cid=$(mysql -N -u${username} -p${password} ${db} -e "$sql");
#[ -z "$cid"] && cid="NONE";


#sql="SELECT count(*)  as attempt  FROM ${db}.im_rsyslog WHERE log_datetime between DATE_SUB('$dt', INTERVAL 10 MINUTE) and '$dt' AND log_message LIKE '%attempt%' and log_ipaddr like '$ip' having attempt >= 3";
#attempt=$(mysql -N -u${username} -p${password} ${db} -e "$sql");
#[ -z "$attempt=" ] && attempt=0;

#if [ $attempt -ge 3 ] ; then
  ##check 
  #sql="SELECT  cus_code  FROM ${db}.im_monitors WHERE cus_code = '$cid' AND is_released = 0 ORDER BY id DESC";
  #chk=$(mysql -N -u${username} -p${password} ${db} -e "$sql")
  #if [ -z "$chk" ] ; then
  #  sql="INSERT into ${db}.im_monitors (cus_code,monitor_type,monitor_msg,created,is_released) VALUES ('$cid','SYSTEM','$msg',now(),0);";
    #echo -e "$sql\n$msg" >> /tmp/xxx
  #  mysql -u${username} -p${password} ${db} -e "$sql"
  #fi
#fi

#fi


fi
