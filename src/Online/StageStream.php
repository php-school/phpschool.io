<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online;

/**
 * Tells the frontend which stage a run or verify is at while the student waits, one JSON line at a time, eg
 * {"stage":"student-execute"}. The action's response follows as the last line.
 */
class StageStream
{
    public function send(string $stage): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/x-ndjson');
            //or nginx holds every line back until the end
            header('X-Accel-Buffering: no');
        }

        echo json_encode(['stage' => $stage]) . "\n";

        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        flush();
    }
}
