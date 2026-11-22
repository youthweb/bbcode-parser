#!/bin/sh
# Fails when the line coverage in the Clover report is below the value in .coverage-floor.
# Usage: scripts/check-coverage-floor.sh [clover.xml] [floor file]
# The floor only climbs: raise it in a reviewed commit, never lower it to make a run pass.
set -eu

clover="${1:-build/logs/clover.xml}"
floor_file="${2:-.coverage-floor}"

if [ ! -f "$clover" ]; then
	echo "error: $clover not found. Run PHPUnit with a coverage driver first." >&2
	exit 2
fi

floor=$(tr -d '[:space:]' <"$floor_file")
case "$floor" in
	''|*[!0-9.]*|*.*.*)
		echo "error: $floor_file must hold one number, for example 62.5." >&2
		exit 2
		;;
esac

metrics=$(grep -o '<metrics files="[^>]*>' "$clover" | tail -n 1)
statements=$(echo "$metrics" | sed -n 's/.* statements="\([0-9]*\)".*/\1/p')
covered=$(echo "$metrics" | sed -n 's/.* coveredstatements="\([0-9]*\)".*/\1/p')

if [ -z "$statements" ] || [ -z "$covered" ] || [ "$statements" -eq 0 ]; then
	echo "error: no statement metrics found in $clover." >&2
	exit 2
fi

awk -v covered="$covered" -v statements="$statements" -v floor="$floor" 'BEGIN {
	pct = covered * 100 / statements
	printf "Line coverage: %.2f%% (%d/%d), floor: %s%%\n", pct, covered, statements, floor
	if (pct + 1e-9 < floor) {
		print "error: coverage fell below the floor." > "/dev/stderr"
		exit 1
	}
}'
