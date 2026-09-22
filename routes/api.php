use App\Http\Controllers\TrajetController;

Route::post('/trajets', [TrajetController::class, 'store']);
Route::get('/trajets', [TrajetController::class, 'index']);
Route::get('/trajets/search', [TrajetController::class, 'search']);
Route::put('/trajets/{id}/localisation', [TrajetController::class, 'updateLocalisation']);
