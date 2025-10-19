<?php

use Illuminate\Support\Facades\Schedule;

// Run the currency update command daily at midnight.
Schedule::command('currency:fetch-rates')->daily()->at('00:00');
