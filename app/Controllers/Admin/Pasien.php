namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PasienModel;

class Pasien extends BaseController
{
    protected $pasienModel;

    public function __construct()
    {
        $this->pasienModel = new PasienModel();
    }

    public function index()
    {
        $data['pasien'] = $this->pasienModel->findAll();
        return view('admin/pasien/index', $data);
    }

    // method create(), store(), edit(), update(), delete() harus dibuat juga
}
