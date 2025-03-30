function timeUpdater(){
    var currentTime = new Date();
    var hours = currentTime.getHours();
    var minutes = currentTime.getMinutes();
    var morning = "AM";
    var afternoon = "PM";


    // Makes sure there are two digits in minutes
    if (minutes < 10) minutes = '0' + minutes;

    if (hours === 12) {
        timeString = "The time is: " + hours + ":" + minutes + " " + afternoon;
    }
    else if (hours > 12) {
        hours = hours - 12;
        timeString = "The time is: " + hours + ":" + minutes + " " + afternoon;
    }
    else {
        var timeString = "The time is: " + hours + ":" + minutes + " " + morning;
    }
    document.getElementById('timeValue').innerText =  timeString;
}

window.onload = function() {
    setInterval(timeUpdater, 1000);
}
