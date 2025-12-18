<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
class User_connecte extends Authenticatable
{
    use HasFactory;

 
    protected $table = 'user_connecte';

    protected $primaryKey = 'id_user_connecte';

    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'prenom_user_connecte',
        'courriel_user_connecte',
        'surnom_user_connecte',
        'date_de_naissance_user_connecte',
        'pays_de_naissance_user_connecte',
        'favori_user_connecte',
        'langue_user_connecte',
        'password_user_connecte',
        'numero_telephone_user_connecte',
        'mfa_active',
        'mfa_code',
        'mfa_expiration'
    ];
    public function getAuthPassword()
    {
        return $this->password_user_connecte;
    }
    public function getAuthIdentifierName()
    {
        return $this->primaryKey;
    }
    public function professionnel()
{
    return $this->hasOne(Professionnel::class, 'id_user_connecte');
}
}
