<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title></title>

    <link rel="stylesheet" href="{{ asset('css/product.css') }}">

    <style>
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .product-detail { display: flex; gap: 40px; margin-top: 30px; }
        .img-box img { width: 100%; max-width: 500px; border-radius: 10px; }
        .info-box { flex: 1; }
        .price { font-size: 24px; color: #b12727; font-weight: bold; margin: 20px 0; }
        .desc { line-height: 1.6; color: white; font-size: 18px; }
        .btn-back { display: inline-block; margin-bottom: 20px; color: black; text-decoration: underline; }
        .choice label { display:block; margin-top:5px; }
        .input-label-produit {             display: block;
            font-size: 14px;
            font-weight: 600;
            color: black;
            margin-bottom: 8px;}
            .middle {

  
  font: 1.2em sans-serif;

  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
            }
            .custom-input {
            width: 100%;
            padding: 12px 0;
            border: none;
            border-bottom: 1px solid #d1d1d1; 
            background-color: transparent;
            font-size: 16px;
            font-family: inherit;
            outline: none;
            transition: border-color 0.3s;
        }
        .footer {
    position: absolute;
    bottom: 0;
    left: 50%;            
    transform: translateX(-50%);
    text-align: center;
}
        .login-title{
            margin-top:60%;
            color:black;
        }
        .group{
margin-top:30%;

        }
    </style>
</head>

<body id="body">

<header>
    <div class="logo">Suggerer un produit pour le FIFA Store</div>  
         
</header>

<section>
<div class="middle">
            <div class="login-box">
                <h2 class="login-title">demande de produit</h2>

                <form method="POST" action="{{ route('registerProduct.step1.post') }}">
    @csrf

                    <div  class="group">
                        <label class="input-label-produit">Nom du produit proposé</label>
                        <input type="text"  name="nom_produit_propose" class="custom-input" value="{{ old('nom_produit_propose') }}" required>
                        
                        @error('nom_produit_propose')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                        
                    </div>
                    
                    
                    <div class="group">
                        <label class="input-label-produit">Description du produit</label>
                        <input type="text" name="description_produit_propose" value="{{ old('description_produit_propose') }}" class="custom-input" required>
                       
                    </div>
 

                    <button type="submit" class="btn-login">créer la proposition</button>
                </form>

                



            </div>
        </div>
    </div>


</section>
<footer class="footer">
                    <a href="#"> Conditions d'utilisation</a>
                    <span>|</span>
                    <a href="/privacy_policy"> Respect de la vie privée </a> </footer>



</body>
</html>
