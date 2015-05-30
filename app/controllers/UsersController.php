<?php

class UsersController extends BaseController {

    /**
     * Tampilkan listing User regular
     * @return response
     */
    public function index()
    {
        if(Datatable::shouldHandle())
        {
            $usersArray = Sentry::findAllUsersWithAccess('regular');
            $usersCollection = new Illuminate\Database\Eloquent\Collection($usersArray);

            // buat datatable
            return Datatable::collection($usersCollection)
                ->addColumn('full_name', function ($model) {
                    return $model->first_name . ' ' . $model->last_name;
                })
                ->showColumns('email')
                ->addColumn('last_login', function($model) {
                    return ($model->last_login ? $model->last_login->toDateTimeString() : '');
                })
                ->addColumn('', function ($model) {
                    $html = '<a href="'.route('admin.users.show', ['users'=>$model->id]).'" class="uk-button uk-button-small uk-button-link">Lihat data peminjaman</a> ';
                    $html .= Form::open(array('url' => route('admin.users.destroy', ['users'=>$model->id]), 'method'=>'delete', 'class'=>'uk-display-inline'));
                    $html .= Form::submit('delete', array('class' => 'uk-button uk-button-small'));
                    $html .= Form::close();
                    return $html;
                })
                ->searchColumns('full_name', 'email', 'last_login')
                ->orderColumns('full_name', 'email', 'last_login')
                ->make();
        }
        return View::make('users.index')->withTitle('Member');
    }

    /**
     * Tampilkan detail user
     * @param  int $id
     * @return response
     */
    public function show($id)
    {
        $user = User::find($id);
        return View::make('users.show')->withUser($user)
            ->withTitle("Detail $user->first_name $user->last_name");
    }

    /**
     * Hapus user
     * @param  int $id
     * @return response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return Redirect::route('admin.users.index')
            ->with('successMessage', 'User berhasil dihapus');
    }

}