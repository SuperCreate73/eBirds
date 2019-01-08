#! /usr/bin/env python

import sys
import re

def main():
    print "SET sql_mode='NO_BACKSLASH_ESCAPES';"
    lines = sys.stdin.read().splitlines()
    for line in lines:
        processLine(line)

def processLine(line):
    if (line.startswith("PRAGMA") or line.startswith("BEGIN TRANSACTION;") or
        line.startswith("COMMIT;") or line.startswith("DELETE FROM sqlite_sequence;") or
        line.startswith("INSERT INTO \"sqlite_sequence\"") ):
        return
# line = line.replace("AUTOINCREMENT", "AUTO_INCREMENT")
    line = re.sub( re.compile( r"AUTOINCREMENT", re.IGNORECASE ), "AUTO_INCREMENT", line)
    line = re.sub( re.compile( r"DEFAULT 't'", re.IGNORECASE ), "DEFAULT '1'", line)
    line = re.sub( re.compile( r"DEFAULT 'f'", re.IGNORECASE ), "DEFAULT '0'", line)
    line = re.sub( re.compile( r",[\s]*'t'", re.IGNORECASE ), ",'1'", line)
    line = re.sub( re.compile( r",[\s]*'f'", re.IGNORECASE ), ",'0'", line)
    in_string = False
    newLine = ''
    for c in line:
        if not in_string:
            if c == "'":
                in_string = True
            elif c == '"':
                newLine = newLine + '`'
                continue
        elif c == "'":
            in_string = False
        newLine = newLine + c
    print newLine

if __name__ == "__main__":
    main()
