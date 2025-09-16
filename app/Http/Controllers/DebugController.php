<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class DebugController extends Controller {
    public function scope(Request $r) {
        return 'WILAYAH: '.$r->get('wilayah_id');
    }
}
