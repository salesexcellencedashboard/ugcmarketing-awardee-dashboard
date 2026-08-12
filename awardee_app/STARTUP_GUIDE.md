# AWARDEE System - Startup Guide

## Overview

The AWARDEE system can be started with a simple **double-click** on the `AwardeeStart.app` icon. No need to open VS Code or a terminal - just double-click and the server starts, then your browser opens automatically.

## How It Works

1. **`AwardeeStart.app`** - A macOS application you double-click to start the system
2. **`start.sh`** - The script that starts/stops the PHP development server
3. **`AwardeeStart.applescript`** - Source code for the app (keep it for reference)

## How to Use

### Start the system (double-click)

Simply **double-click** `AwardeeStart.app` on your **Desktop**:

```
/Users/jhonarvin/Desktop/AwardeeStart.app
```

Or from the project folder:
```
/Users/jhonarvin/Desktop/AWARDEE/awardee_app/AwardeeStart.app
```

The app will:
1. Check if the server is already running
2. Start the server if needed (silently in the background)
3. Open the browser automatically to: `http://localhost:8083`

### If the server is already running

Just double-click `AwardeeStart.app` again - it will detect the server is running and just open the browser.

## Manual Commands (Terminal)

If you prefer using the terminal:

```bash
cd /Users/jhonarvin/Desktop/AWARDEE/awardee_app

./start.sh              # Start server in foreground (shows logs)
./start.sh --daemon     # Start server in background (silent)
./start.sh --stop       # Stop the server
./start.sh --status     # Check if server is running
```

## Logs

Server logs are written to:
```
/Users/jhonarvin/Desktop/AWARDEE/awardee_app/.server.log
```

## Requirements

- PHP 8.2 or higher (installed at `/opt/homebrew/bin/php`)
- MySQL database running (the app connects to `awardee_system` database)
- The project must remain at: `/Users/jhonarvin/Desktop/AWARDEE/awardee_app`

## Troubleshooting

### Double-clicking the app doesn't do anything
1. Make sure `AwardeeStart.app` exists in the project folder
2. Right-click the app and select **Open** - macOS may ask for permission the first time
3. If macOS blocks it, go to **System Settings > Privacy & Security** and click **Open Anyway**

### Server won't start
1. Check if PHP is installed: `php -v`
2. Check if MySQL is running
3. Check the log file: `cat /Users/jhonarvin/Desktop/AWARDEE/awardee_app/.server.log`
4. Try starting manually: `./start.sh`

### Port 8083 already in use
The server uses port 8083. If another process is using it, stop that process first or change the port in `start.sh`.

### If the app gets deleted or corrupted
Recompile it using the source script:
```bash
cd /Users/jhonarvin/Desktop/AWARDEE/awardee_app
osacompile -o AwardeeStart.app /tmp/AwardeeStart.app 2>/dev/null || osacompile -o AwardeeStart.app AwardeeStart.applescript