<?php

namespace App\Services;

use App\Models\Event;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    public function generateForEvent(Event $event)
    {
        $url = route('evaluations.form', ['event' => $event->id]);
        
        return QrCode::size(300)
            ->backgroundColor(255, 255, 255)
            ->color(0, 0, 0)
            ->margin(1)
            ->generate($url);
    }

    public function getEvaluationUrl(Event $event)
    {
        return route('evaluations.form', ['event' => $event->id]);
    }
}