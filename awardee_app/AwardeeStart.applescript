-- AWARDEE System - Double-Click to Start
-- This app starts the AWARDEE server and opens it in the browser

-- Check if server is already running
set serverRunning to false
try
    do shell script "curl -s -o /dev/null -w '%{http_code}' http://localhost:8083/"
    if result is "200" then
        set serverRunning to true
    end if
end try

-- Start the server if not running
if not serverRunning then
    do shell script "/Users/jhonarvin/Desktop/AWARDEE/awardee_app/start.sh --daemon"
    delay 2
end if

-- Open the browser
open location "http://localhost:8083"