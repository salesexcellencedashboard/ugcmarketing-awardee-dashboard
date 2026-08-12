#!/bin/bash
# ==============================================================
# AWARDEE System - Startup Script
# ==============================================================
# This script starts the PHP built-in development server for
# the AWARDEE application.
#
# Usage:
#   ./start.sh              - Start server in foreground
#   ./start.sh --daemon     - Start server in background (daemon mode)
#   ./start.sh --stop       - Stop the background server
#   ./start.sh --status     - Check if server is running
# ==============================================================

# Get the directory where this script is located
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PORT=8083
HOST=localhost
PID_FILE="$DIR/.server.pid"
LOG_FILE="$DIR/.server.log"

# Ensure we're in the project directory
cd "$DIR" || { echo "Error: Cannot change to project directory $DIR"; exit 1; }

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "Error: PHP is not installed. Please install PHP 8.2 or higher."
    exit 1
fi

# Check if port is already in use
port_in_use() {
    if lsof -i :"$PORT" -sTCP:LISTEN &>/dev/null; then
        return 0
    fi
    return 1
}

start_daemon() {
    # Check if port is already in use
    if port_in_use; then
        echo "AWARDEE server is already running on port $PORT at http://$HOST:$PORT"
        exit 0
    fi

    echo "Starting AWARDEE server in background..."
    nohup php -S "$HOST:$PORT" -t "$DIR/public" "$DIR/vendor/codeigniter4/framework/system/rewrite.php" > "$LOG_FILE" 2>&1 &
    local pid=$!
    echo $pid > "$PID_FILE"

    # Wait a moment and check if it started
    sleep 2
    if kill -0 $pid 2>/dev/null; then
        echo "AWARDEE server started successfully (PID: $pid)"
        echo "Access the application at: http://$HOST:$PORT"
        echo "Log file: $LOG_FILE"
    else
        echo "Error: Failed to start AWARDEE server."
        echo "Check log file: $LOG_FILE"
        rm -f "$PID_FILE"
        exit 1
    fi
}

stop_daemon() {
    # Kill any process on port 8083
    if port_in_use; then
        local pids
        pids=$(lsof -ti :"$PORT" -sTCP:LISTEN)
        echo "Stopping AWARDEE server (PID: $pids)..."
        kill $pids 2>/dev/null
        sleep 1
        # Force kill if still running
        if port_in_use; then
            kill -9 $pids 2>/dev/null
        fi
        echo "AWARDEE server stopped."
    else
        echo "No running AWARDEE server found on port $PORT."
    fi
    rm -f "$PID_FILE"
}

status() {
    if port_in_use; then
        local pid
        pid=$(lsof -ti :"$PORT" -sTCP:LISTEN | head -1)
        echo "AWARDEE server is RUNNING (PID: $pid)"
        echo "URL: http://$HOST:$PORT"
        return 0
    fi
    echo "AWARDEE server is NOT running."
    return 1
}

# Main logic
case "${1:-}" in
    --daemon|-d)
        start_daemon
        ;;
    --stop|-s)
        stop_daemon
        ;;
    --status|-t)
        status
        ;;
    --help|-h)
        echo "AWARDEE System Startup Script"
        echo ""
        echo "Usage:"
        echo "  ./start.sh              Start server in foreground (Ctrl+C to stop)"
        echo "  ./start.sh --daemon     Start server in background"
        echo "  ./start.sh --stop       Stop background server"
        echo "  ./start.sh --status     Check server status"
        echo "  ./start.sh --help       Show this help"
        exit 0
        ;;
    *)
        # Default: start in foreground
        echo "Starting AWARDEE server at http://$HOST:$PORT"
        echo "Press Ctrl+C to stop the server."
        echo ""
        php -S "$HOST:$PORT" -t "$DIR/public" "$DIR/vendor/codeigniter4/framework/system/rewrite.php"
        ;;
esac