create a new file (password.php) with the content:
<?php
$PASSWORD = "YOUR-PASSWORD";

deviceinfo.php contains the getter and setter.
e.g. deviceinfo.php?s=b&v=73&c&p=YOUR-PASSWORD
sets the battery (s=b) to value 73 (v=73) and the state to charging (c)
The password is required in setter requests.
deviceinfo.php?g=b gets the battery.

history.php contains a line chart of your battery history.

![Preview of history](http://i.dv.tl/Screenshot_2015-09-02_at_22.25.40.png)