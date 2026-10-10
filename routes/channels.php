<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('detections', function () {
    return true;
});
