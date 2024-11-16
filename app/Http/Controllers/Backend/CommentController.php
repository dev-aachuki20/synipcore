<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\CommentDataTable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Post\PostRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CommentDataTable $dataTable)
    {
        abort_if(Gate::denies('comment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.comment.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        abort_if(Gate::denies('comment_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            try {
                $comment = Comment::FindOrFail($id);
                $viewHTML = view('backend.comment.show', compact('comment'))->render();
                return response()->json(array('success' => true, 'htmlView' => $viewHTML));
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        abort_if(Gate::denies('comment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $comment = Comment::FindOrFail($id);
                if ($comment) {
                    $comment->delete();
                }

                DB::commit();
                return response()->json([
                    'success'    => true,
                    'message'    => trans('messages.deleted_successfully'),
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }


    public function changeStatus(Request $request)
    {
        abort_if(Gate::denies('comment_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), ['id' => ['required','exists:comments,id',]]);
            if (!$validator->passes()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag()->toArray(), 'message' => 'Error Occured!'], 400);
            } else {
                DB::beginTransaction();
                try {
                    $comment = Comment::FindOrFail($request->id);
                    if ($comment->is_approve == 0) {
                        $is_approve = 1;
                    } else {
                        $is_approve = 0;
                    }
                    $comment->update(['is_approve' => $is_approve]);

                    DB::commit();
                    $response = [
                        'status'    => 'true',
                        'message'   => trans('messages.status_update_successfully'),
                    ];
                    return response()->json($response);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
                }
            }
        }
    }
}
