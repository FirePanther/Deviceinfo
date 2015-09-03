This daemon sends your current battery level and state to the server.
You have to install sbutils and wget from cydia.

Add your password and your url in deviceinfo.sh.

Move deviceinfo.sh into the `/User/Documents` folder on your iDevice and the plist file into `/Library/LaunchDaemons`.
Give execution permissions to the shell file with: `chmod +x /User/Documents/deviceinfo.sh`
Install the launchdaemon with this command (e.g. with MTerminal or ssh connection):
`launchctl load /Library/LaunchDaemons/pro.firepanther.deviceinfo.plist`