<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * API UserContoller Test
 * Class ApiUserTest
 * @package Tests\Feature
 */
class ApiUserTest extends TestCase
{

    /**
     * テスト：users/複数ユーザ
     */
    public function testUsers()
    {
        // ユーザーを作成
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->get('/api/v1/user/users')
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $user->id,
                'username' => $user->username,
                'display_name' => $user->display_name,
            ]);

    }

    /**
     * テスト：users/単一ユーザ
     */
    public function testUsers2()
    {
        // ユーザーを作成
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->get('/api/v1/user/users', [
            'id' => $user->id,
        ])
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $user->id,
                'username' => $user->username,
                'display_name' => $user->display_name,
            ]);

    }

    /**
     * テスト：edit
     */
    public function testEdit()
    {
        // ユーザーを作成
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->post('/api/v1/user/edit', [
            'id' => $user->id,
            'username' => $user->username,
            'display_name' => $user->display_name . 'edited',
            'email' => $user->email,
            'avatar_type' => 1,
        ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

    }

    /**
     * テスト：passwordChange
     */
    public function testPasswordChange()
    {

        // ユーザーを作成
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->post('/api/v1/user/passwordChange', [
            'password' => 'password01',
            'newPassword' => 'password01_edit',
            'newPassword_confirmation' => 'password01_edit',
        ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

    }

    /**
     * テスト：passwordChange/現在のパスワードが違う
     */
    public function testPasswordChangeOnFail()
    {

        // ユーザーを作成
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->post('/api/v1/user/passwordChange', [
            'password' => 'password01_dummy',
            'newPassword' => 'password01_edit',
            'newPassword_confirmation' => 'password01_edit',
        ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'error',
            ]);

    }

    /**
     * テスト：uploadAvatar
     */
    public function testUploadAvatar()
    {

        // ユーザーを作成
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->post('/api/v1/user/uploadAvatar', [
            'avatar_file' => UploadedFile::fake()->image('avatar.jpg'),
        ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $dbUser = \App\User::find($user->id);

        // ToDo: アップロードの確認が出来ない

        // ファイルが保存されたことをアサートする
//        Storage::disk('avatars')->assertExists($dbUser->avatar_filename);

    }

    /**
     * テスト：uploadAvatar Fail/画像サイズ超過
     */
    public function testUploadAvatarOnFail()
    {

        // ユーザーを作成
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->post('/api/v1/user/uploadAvatar', [
            'avatar_file' => UploadedFile::fake()->image('avatar.jpg', 641, 481),
        ])
            ->assertStatus(422);

        //        Storage::disk('avatars')->assertMissing('avatar.jpg');

    }

    /**
     * テスト：uploadAvatar Fail/画像以外
     */
    public function testUploadAvatarOnFail2()
    {

        // ユーザーを作成
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->post('/api/v1/user/uploadAvatar', [
            'avatar_file' => UploadedFile::fake()->create('avatar.pdf'),
        ])
            ->assertStatus(422);

//        Storage::disk('avatars')->assertMissing('avatar.pdf');

    }

}
