<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\AdminMessageDataTable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;


class AdminMessageController extends Controller
{
    public function index(AdminMessageDataTable $dataTable)
    {
        abort_if(Gate::denies('admin_message_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.admin-message.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }
}
