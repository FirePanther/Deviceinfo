import Cocoa

@NSApplicationMain
class AppDelegate: NSObject, NSApplicationDelegate {
    
    @IBOutlet weak var window: NSWindow!
    
    let statusItem = NSStatusBar.systemStatusBar().statusItemWithLength(-1)
    
    func applicationDidFinishLaunching(notification: NSNotification) {
        statusItem.menu = NSMenu()
        statusItem.menu!.addItem(NSMenuItem(title: "Quit", action: Selector("quit"), keyEquivalent: "q"))
        
        updateIcon()
        NSTimer.scheduledTimerWithTimeInterval(10, target: self, selector: "updateIcon", userInfo: nil, repeats: true)
    }
    
    func quit() {
        NSApplication.sharedApplication().terminate(self)
    }
    
    func updateIcon() {
        let url = NSURL(string: "http://YOUR-URL/deviceinfo.php?g=b")
        
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            var val = String(NSString(data: data, encoding: NSUTF8StringEncoding)!)
            self.statusItem.button!.image = NSImage(named: val)
        }
        
        task.resume()
    }
    
    func applicationWillTerminate(aNotification: NSNotification) {}
}

