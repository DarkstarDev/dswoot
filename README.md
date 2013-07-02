So I've finally gotten around to writing Darkstar's Woot!-off Checker v3.

The new version is written using Zend Framework 1.11.11.

##FAQ##
1. **Does it have any of the features you promised?**
    No.
2. **But you said it would have a flash version, an HTML5 version, and a basic version!**
    I lied, I guess.  It fetches the current woot item and saves it to a database though.  That's a little more useful than before.
3. **Does it at least predict when an item will sell out?**
    No.  Not yet, anyway.  The database does keep track of the percent sold at the time of refresh, though, so it's possible to extrapolate sellout time  once you have enough samples.
4. **So what the hell have you been doing this whole time?**
    Not much, actually.  Working, mostly.  I sat down last year (or 2 years ago) and planned out features for the woot-checker but never got around to actually building them.  I've since decided to take the 37Signals approach and build only the basic features, in this case it needs to fetch and cache from woot.
5. **This sounds absolutely useless.**
    So don't use it.
6. **But I want to use it.  Give it more features!**
    When I have time I will.  Until then, don't use it.
7. **What DOES this thing do?**
    It can fetch from cache, check for staleness and fetch from woot if the cache is stale.  It can fetch from woot, shirt.woot, wine.woot, sellout.woot, kids.woot, and moofi.  It can also show you a historical list of items sold on woot and their progress over time.
8. **What about the Android and iOS apps you promised?**
    You're not paying attention, are you?
9. **What works right now?**
    Fetching from Woot!, caching, and fetching said cache.  It will also fetch the images for each item if they're not already on the disk.  It will also implement a lockout to only allow one user to refresh the cache from Woot! to avoid hammering their servers.
10. **Did anybody actually ask you ANY of these questions?**
    Nope.

##INSTALLATION##
For installation instructions, see https://github.com/DarkstarDev/dswoot/wiki/Installation-Instructions

##UPGRADING##
For upgrade instructions, see https://github.com/DarkstarDev/dswoot/wiki/Upgrading
