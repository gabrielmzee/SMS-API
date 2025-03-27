<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\NIDAController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\YearController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\StaffFrController;
use App\Http\Controllers\StaffTzController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ExamTermController;
use App\Http\Controllers\TermYearController;
use App\Http\Controllers\ClassYearController;
use App\Http\Controllers\StreamClassController;
use App\Http\Controllers\SubjectClassController;

Route::post('register-user', [AuthController::class, 'register']);

Route::get('email/verify/{id}/{hash}', function ($id, $hash, Request $request) {
    $user = User::findOrFail($id);

    if ($user->hasVerifiedEmail() || sha1($user->getEmailForVerification()) !== $hash) {
        return response()->json(['message' => 'Email verification failed'], 400);
    }

    $user->markEmailAsVerified();
    $user->update(['is_active' => true]);

    return response()->json(['message' => 'Email successfully verified'], 200);
})->name('verification.verify');

Route::post('email/verification-notification', function (Request $request) {
    $user = $request->user();
    
    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified'], 400);
    }

    $user->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification link sent!'], 200);
});


Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);

    Route::prefix('years')->group(function () {
        Route::get('/', [YearController::class, 'index']);
        Route::get('/{year}', [YearController::class, 'show']);
        Route::post('/', [YearController::class, 'store']);
        Route::put('/{year}', [YearController::class, 'update']);
        Route::delete('/{year}', [YearController::class, 'destroy']);
    });

    Route::prefix('subjects')->group(function () {
        Route::get('/', [SubjectController::class, 'index']);
        Route::get('/{subject}', [SubjectController::class, 'show']);
        Route::post('/', [SubjectController::class, 'store']);
        Route::put('/{subject}', [SubjectController::class, 'update']);
        Route::delete('/{subject}', [SubjectController::class, 'destroy']);
    });

    Route::prefix('classes')->group(function () {
        Route::get('/', [ClassController::class, 'index']);
        Route::get('/{class}', [ClassController::class, 'show']);
        Route::post('/', [ClassController::class, 'store']);
        Route::put('/{class}', [ClassController::class, 'update']);
        Route::delete('/{class}', [ClassController::class, 'destroy']);
    });

    Route::prefix('streams')->group(function () {
        Route::get('/', [StreamController::class, 'index']);
        Route::get('/{stream}', [StreamController::class, 'show']);
        Route::post('/', [StreamController::class, 'store']);
        Route::put('/{stream}', [StreamController::class, 'update']);
        Route::delete('/{stream}', [StreamController::class, 'destroy']);
    });

    Route::prefix('terms')->group(function () {
        Route::get('/', [TermController::class, 'index']);
        Route::get('/{term}', [TermController::class, 'show']);
        Route::post('/', [TermController::class, 'store']);
        Route::put('/{term}', [TermController::class, 'update']);
        Route::delete('/{term}', [TermController::class, 'destroy']);
    });

    Route::prefix('class-year')->group(function () {
        Route::get('/{year}', [ClassYearController::class, 'index']);
        Route::get('/view/{class_year}', [ClassYearController::class, 'show']);
        Route::post('/', [ClassYearController::class, 'store']);
        Route::delete('/{class_year}', [ClassYearController::class, 'destroy']);
    });

    Route::prefix('term-year')->group(function () {
        Route::get('/{year}', [TermYearController::class, 'index']);
        Route::get('/view/{term_year}', [TermYearController::class, 'show']);
        Route::post('/', [TermYearController::class, 'store']);
        Route::delete('/{term_year}', [TermYearController::class, 'destroy']);
    });

    Route::prefix('stream-class')->group(function () {
        Route::get('/{class}', [StreamClassController::class, 'index']);
        Route::get('/view/{stream_class}', [StreamClassController::class, 'show']);
        Route::post('/', [StreamClassController::class, 'store']);
        Route::put('/{stream_class}', [StreamClassController::class, 'update']);
        Route::delete('/{stream_class}', [StreamClassController::class, 'destroy']);
    });

    Route::prefix('subject-class')->group(function () {
        Route::get('/{class}', [SubjectClassController::class, 'index']);
        Route::get('/view/{subject_class}', [SubjectClassController::class, 'show']);
        Route::post('/', [SubjectClassController::class, 'store']);
        Route::put('/{subject_class}', [SubjectClassController::class, 'update']);
        Route::delete('/{subject_class}', [SubjectClassController::class, 'destroy']);
    });

    Route::prefix('activate-deactivate')->group(function () {
        Route::put('/year/{year}', [YearController::class, 'activate_deactivate']);
        Route::put('/class/{class}', [ClassController::class, 'activate_deactivate']);
        Route::put('/term/{term}', [TermController::class, 'activate_deactivate']);
        Route::put('/stream/{stream}', [StreamController::class, 'activate_deactivate']);
        Route::put('/staff/{staff}', [StaffController::class, 'activate_deactivate']);
    });

    Route::post('/NIDA-Verification', [NIDAController::class, 'verification']);

    Route::prefix('staff')->group(function () {
        Route::get('/', [StaffTzController::class, 'index']);
        Route::get('/tz/{staff}', [StaffTzController::class, 'show']);
        Route::post('/tz', [StaffTzController::class, 'store']);
        Route::put('/tz/{staff}', [StaffTzController::class, 'update']);
        Route::delete('/tz/{staff}', [StaffTzController::class, 'destroy']);
        Route::get('/fr/{staff}', [StaffFrController::class, 'show']);
        Route::post('/fr', [StaffFrController::class, 'store']);
        Route::put('/fr/{staff}', [StaffFrController::class, 'update']);
        Route::delete('/fr/{staff}', [StaffFrController::class, 'destroy']);
    });


    Route::prefix('exams')->group(function () {
        Route::get('/', [ExamController::class, 'index']);
        Route::get('/{exam}', [ExamController::class, 'show']);
        Route::post('/', [ExamController::class, 'store']);
        Route::put('/{exam}', [ExamController::class, 'update']);
        Route::delete('/{exam}', [ExamController::class, 'destroy']);
    });

    Route::prefix('exams-term')->group(function () {
        Route::get('/{term}', [ExamTermController::class, 'index']);
        Route::get('/{exam_term}', [ExamTermController::class, 'show']);
        Route::post('/', [ExamTermController::class, 'store']);
        Route::put('/{exam_term}', [ExamTermController::class, 'update']);
        Route::delete('/{exam_term}', [ExamTermController::class, 'destroy']);
    });

});



