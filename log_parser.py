import re
import sys
import json
from datetime import datetime
from collections import defaultdict

LOG_PATTERN = re.compile(
    r'(?P<ip>\d{1,3}(?:\.\d{1,3}){3})'
    r'\s-\s-\s'
    r'\[(?P<timestamp>[^\]]+)\]'
    r'\s"(?P<request>[^"]+)"'
    r'\s(?P<status>\d{3})'
)

SQLI_HIGH = [
    r"union\s+select", r"drop\s+table", r"insert\s+into",
    r"update\s+\w+\s+set", r"sleep\s*\(", r"benchmark\s*\(",
]
SQLI_MEDIUM = [
    r"'\s*or\s*'?1'?\s*=\s*'?1", r"\bor\s+1\s*=\s*1\b", r"--", r";\s*--", r"'\s*;",
]
XSS_HIGH = [r"<script[^>]*>", r"on\w+\s*=", r"javascript\s*:"]
XSS_MEDIUM = [r"<img[^>]*>", r"<iframe[^>]*>", r"alert\s*\("]


def fix_time(raw):
    try:
        dt = datetime.strptime(raw, "%d/%b/%Y:%H:%M:%S %z")
        return dt.strftime("%Y-%m-%d %H:%M:%S")
    except ValueError:
        return raw


def read_log(path):
    lines = []
    with open(path, "r", encoding="utf-8", errors="ignore") as f:
        for line in f:
            line = line.strip()
            if not line:
                continue
            m = LOG_PATTERN.search(line)
            if not m:
                continue
            lines.append({
                "ip": m.group("ip"),
                "timestamp": fix_time(m.group("timestamp")),
                "request": m.group("request"),
                "status": m.group("status"),
            })
    return lines


def check_patterns(text, high_list, medium_list, case_insensitive=False):
    flags = re.IGNORECASE if case_insensitive else 0
    search_text = text.lower() if not case_insensitive else text
    for p in high_list:
        if re.search(p, search_text if not case_insensitive else text, flags):
            return "High"
    for p in medium_list:
        if re.search(p, search_text if not case_insensitive else text, flags):
            return "Medium"
    return None


def find_sqli_xss(lines):
    threats = []
    for row in lines:
        req = row["request"]
        sqli = check_patterns(req, SQLI_HIGH, SQLI_MEDIUM)
        if sqli:
            threats.append({
                "ip_address": row["ip"],
                "detected_at": row["timestamp"],
                "threat_type": "SQL Injection",
                "severity": sqli,
            })
            continue
        xss = check_patterns(req, XSS_HIGH, XSS_MEDIUM, case_insensitive=True)
        if xss:
            threats.append({
                "ip_address": row["ip"],
                "detected_at": row["timestamp"],
                "threat_type": "XSS",
                "severity": xss,
            })
    return threats


def find_brute_force(lines, min_attempts=3):
    fails = defaultdict(list)
    for row in lines:
        if row["status"] in ("401", "403"):
            fails[row["ip"]].append(row)

    threats = []
    for ip, attempts in fails.items():
        count = len(attempts)
        if count < min_attempts:
            continue
        severity = "Critical" if count >= 8 else "High" if count >= 5 else "Medium"
        last = attempts[-1]
        threats.append({
            "ip_address": ip,
            "detected_at": last["timestamp"],
            "threat_type": "Brute Force",
            "severity": severity,
        })
    return threats


def parse_log_file(path):
    lines = read_log(path)
    threats = find_sqli_xss(lines) + find_brute_force(lines)
    return threats


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Usage: python log_parser.py <log_file>"}))
        sys.exit(1)
    result = parse_log_file(sys.argv[1])
    print(json.dumps(result))
