<?php

use Illuminate\Support\Facades\Broadcast;

// Public channel — no auth required (accessible by anyone with the document ID)
Broadcast::channel('document.{id}', fn() => true);
