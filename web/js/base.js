
function   formatDate(timestamp)   {
    var   now = new  Date(timestamp);
    var   year=now.getYear();
    var   month=now.getMonth()+1;
    var   date=now.getDate();
    var   hour=now.getHours();
    var   minute=now.getMinutes();
    var   second=now.getSeconds();
    return  month+"-"+date+" "+hour+"时";
}

