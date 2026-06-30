# Import regular expressions module
import re
# Import system module for exit and argv
import sys
# Import json module for input/output handling
import json
# Import datetime module for parsing timestamps
from datetime import datetime
# Import defaultdict for grouping failed login attempts
from collections import defaultdict

# Pre-compile Apache/Nginx standard log parser pattern
LOG_PATTERN = re.compile(
    r'(?P<ip>\d{1,3}(?:\.\d{1,3}){3})' # Capture source IP address
    r'\s-\s-\s' # Matches standard Apache spacer
    r'\[(?P<timestamp>[^\]]+)\]' # Capture request timestamp string
    r'\s"(?P<request>[^"]+)"' # Capture HTTP request line
    r'\s(?P<status>\d{3})' # Capture HTTP response status code
)

# Compiled high-severity SQL injection regex pattern
SQLI_HIGH = re.compile(r"union\s+select|drop\s+table|insert\s+into|update\s+\w+\s+set|sleep\s*\(|benchmark\s*\(", re.I)
# Compiled medium-severity SQL injection regex pattern
SQLI_MEDIUM = re.compile(r"'\s*or\s*'?1'?\s*=\s*'?1|\bor\s+1\s*=\s*1\b|--|;\s*--|'\s*;", re.I)
# Compiled high-severity Cross-Site Scripting (XSS) regex pattern
XSS_HIGH = re.compile(r"<script[^>]*>|on\w+\s*=|javascript\s*:", re.I)
# Compiled medium-severity Cross-Site Scripting (XSS) regex pattern
XSS_MEDIUM = re.compile(r"<img[^>]*>|<iframe[^>]*>|alert\s*\(", re.I)

# Helper function to convert log time string to standard format
def fix_time(raw):
    try: # Try to parse raw timestamp with timezone offset
        return datetime.strptime(raw, "%d/%b/%Y:%H:%M:%S %z").strftime("%Y-%m-%d %H:%M:%S")
    except ValueError: # Fallback to return raw string if format does not match
        return raw

# Read log file and yield structured dictionary matching LOG_PATTERN
def read_log(path):
    lines = [] # Initialize storage list for parsed log lines
    with open(path, "r", encoding="utf-8", errors="ignore") as f: # Open file safely ignoring encoding errors
        for line in f: # Loop through each raw line in file
            m = LOG_PATTERN.search(line.strip()) # Match pattern on stripped line
            if m: # If line successfully matched pattern
                lines.append({ # Append parsed fields to lines list
                    "ip": m.group("ip"), # Extract source IP
                    "timestamp": fix_time(m.group("timestamp")), # Parse and format timestamp
                    "request": m.group("request"), # Extract HTTP request payload
                    "status": m.group("status"), # Extract response status
                })
    return lines # Return populated list of parsed log entries

# Scan request lines and identify SQLi and XSS security threat events
def find_sqli_xss(lines):
    threats = [] # Initialize list to store detected SQLi and XSS threats
    for row in lines: # Iterate through each parsed log record
        req = row["request"] # Retrieve the HTTP request string
        if SQLI_HIGH.search(req): # Check if request matches high SQLi pattern
            threats.append({"ip_address": row["ip"], "detected_at": row["timestamp"], "threat_type": "SQL Injection", "severity": "High"})
        elif SQLI_MEDIUM.search(req): # Check if request matches medium SQLi pattern
            threats.append({"ip_address": row["ip"], "detected_at": row["timestamp"], "threat_type": "SQL Injection", "severity": "Medium"})
        elif XSS_HIGH.search(req): # Check if request matches high XSS pattern
            threats.append({"ip_address": row["ip"], "detected_at": row["timestamp"], "threat_type": "XSS", "severity": "High"})
        elif XSS_MEDIUM.search(req): # Check if request matches medium XSS pattern
            threats.append({"ip_address": row["ip"], "detected_at": row["timestamp"], "threat_type": "XSS", "severity": "Medium"})
    return threats # Return list of detected payload threats

# Scan log events to detect brute force attacks based on failed login attempts
def find_brute_force(lines, min_attempts=3):
    fails = defaultdict(list) # Initialize dictionary to collect failed requests by IP
    for row in lines: # Iterate through all log records
        if row["status"] in ("401", "403"): # Filter out unauthorized or forbidden responses
            fails[row["ip"]].append(row) # Track failed attempt under IP address
    threats = [] # Initialize list for brute force threat alerts
    for ip, attempts in fails.items(): # Process gathered failed attempts for each IP
        count = len(attempts) # Count the total number of failures
        if count >= min_attempts: # Check if threshold of failed attempts is met
            severity = "Critical" if count >= 8 else "High" if count >= 5 else "Medium" # Determine severity based on attempt volume
            threats.append({ # Append new brute force threat record
                "ip_address": ip, # Store target IP address
                "detected_at": attempts[-1]["timestamp"], # Use timestamp of latest failure
                "threat_type": "Brute Force", # Set threat class
                "severity": severity, # Set evaluated severity level
            })
    return threats # Return gathered brute force threat events

# Parse log file contents and return combined threat occurrences list
def parse_log_file(path):
    lines = read_log(path) # Read and parse log file
    return find_sqli_xss(lines) + find_brute_force(lines) # Combine and return all threat alerts

# Script entry point block
if __name__ == "__main__":
    if len(sys.argv) < 2: # Ensure log file path argument is supplied
        print(json.dumps({"error": "Usage: python log_parser.py <log_file>"})) # Print correct usage instruction
        sys.exit(1) # Exit process with failure code
    print(json.dumps(parse_log_file(sys.argv[1]))) # Parse file and output resulting JSON to console
