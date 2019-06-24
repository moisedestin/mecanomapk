<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    {{--<link href="{{ asset('css/app.css') }}" rel="stylesheet">--}}

    <style>
        body{
            color:black;
            background-color: white;
        }

        @media (min-width: 576px){
            .container {
                max-width: 540px;
            }
        }

        @media (min-width: 768px){
            .container {
                max-width: 720px;
            }
        }
            @media (min-width: 992px){
                .container {
                    max-width: 960px;
                }
            }





        @media (min-width: 1200px){
            .container {
                max-width: 1140px;
            }
        }

                        .container {
                            width: 100%;
                            padding-right: 15px;
                            padding-left: 15px;
                            margin-right: auto;
                            margin-left: auto;
                        }
    </style>
</head>
<body >
<p><strong>Infos</strong></p>
<ul>
    <li><strong><strong>A propos</strong></strong></li>
</ul>
<p>Mecano'm est une application mobile d&eacute;velopp&eacute;e pour faciliter le d&eacute;pannage de voitures en quelques minutes &agrave; peine. Elle donne acc&egrave;s &agrave; un r&eacute;pertoire de m&eacute;caniciens exp&eacute;riment&eacute;s &agrave; proximit&eacute;, pour une r&eacute;paration de pannes de voiture en toute tranquillit&eacute; d'esprit.</p>
<p>Que vous soyez dans les centres villes d'Ha&iuml;ti ou ailleurs, Mecano'm est toujours &agrave; port&eacute;e de vous. T&eacute;l&eacute;chargez l&rsquo;appli et commandez une course d&egrave;s aujourd&rsquo;hui.</p>
<p>L'application vous permettra d'effectuer les op&eacute;rations suivantes:</p>
<ol>
    <li>R&eacute;pertorier des m&eacute;caniciens exp&eacute;riment&eacute;s dans votre entourage</li>
    <li>Solliciter une demande de d&eacute;pannage &agrave; un des m&eacute;caniciens disponibles</li>
    <li>Solliciter une demande de remorquage en cas de besoin</li>
</ol>
<ul>
    <li><strong><strong>Votre profil</strong></strong></li>
</ul>
<p>En vous inscrivant sur l'application Mecano'm, vous acceptez de nous fournir des informations personnelles telles que votre email, un mot de passe, des informations sur votre v&eacute;hicule et votre localisation. Votre inscription souscrit &agrave; l'acceptation de notre politique de confidentialit&eacute; ainsi que l'utilisation des cookies dans le but de vous offrir de meilleurs services. Vous disposez &agrave; tout moment d'un droit d'acc&egrave;s, de rectification et suppression relativement aux donn&eacute;es vous concernant dans les limites pr&eacute;vues par la loi. </p>
<ul>
    <li><strong><strong>Nos agents</strong></strong></li>
</ul>
<p>Nous accordons une attention particuli&egrave;re au recrutement des m&eacute;caniciens sur notre plateforme. Chacun d&eacute;montre des exp&eacute;riences concluantes pour faire partie de notre r&eacute;seau. Ils ont aussi re&ccedil;u une formation chez nous pour mieux faciliter la communication avec vous. Nos agents ont sign&eacute; des termes de confidentialit&eacute;s et une garantie sur la qualit&eacute; des services qu&acute;ils vous fourniront.</p>
<p>Pour obtenir de services plus efficients, nous vous conseillons de toujours v&eacute;rifier les comp&eacute;tences et les avis fournis sur nos agents &agrave; travers leur profil. </p>
<ul>
    <li><strong><strong>Voici comment faire une demande de d&eacute;pannage :</strong></strong></li>
</ul>
<p>- Ouvrez l&rsquo;appli et indiquez le service que vous souhaitez obtenir.</p>
<p>- L&rsquo;appli utilise votre localisation pour que l'agent que vous avez choisi puisse vous rejoindre l&agrave; o&ugrave; vous &ecirc;tes.</p>
<p>- Vous verrez une photo de l'agent sur son profil, ses expertises, et vous pourrez suivre son avanc&eacute;e.</p>
<p>- Si vous souhaitez faire une demande, il vous faut d'abord s&eacute;lectionner un agent disponible, pr&eacute;f&eacute;rablement le plus pr&egrave;s de vous. Lorsque vous effectuez une demande &agrave; un de nos agents, vos demandes seront trait&eacute;es que si vous consentez de payer ses frais de d&eacute;placement qui vous sera communiqu&eacute;s une que vous ayez &eacute;mis la requ&ecirc;te. </p>
<p>- Vous devez payer en esp&egrave;ces &agrave; l'agent, une fois qu'il est sur place.</p>
<p>- Apr&egrave;s chaque op&eacute;ration, vous recevrez une notification sollicitant votre avis pour nous aider &agrave; am&eacute;liorer l&rsquo;exp&eacute;rience Mecano'm. </p>
<p>Remarque: un usage continu du GPS en arri&egrave;re-plan peut faire baisser de mani&egrave;re importante la dur&eacute;e de vie de la batterie de votre t&eacute;l&eacute;phone.</p>
<p><br /> </p>
<p></p>
<p><strong>TERMES ET CONDITIONS D&acute;UTILISATION</strong></p>
<ul>
    <li><strong><strong>La politique de donn&eacute;es</strong></strong></li>
</ul>
<p>La fourniture de notre service n&eacute;cessite la collecte et l'utilisation de vos informations. La politique de donn&eacute;es explique comment nous recueillons, utilisons vos informations. Il explique &eacute;galement les nombreuses fa&ccedil;ons dont vous pouvez contr&ocirc;ler vos informations. Vous devez accepter la politique de donn&eacute;es pour utiliser Mecano'm.</p>
<ul>
    <li><strong><strong>Vos engagements</strong></strong></li>
</ul>
<p>En &eacute;change de notre engagement &agrave; fournir le Service, nous vous demandons de prendre les engagements ci-dessous envers nous. Pourvu que nous voulons que notre service soit aussi ouvert et inclusif que possible, et &eacute;galement qu'il soit s&ucirc;r et s&eacute;curis&eacute; et conforme &agrave; la loi. Nous devons donc vous engager &agrave; respecter quelques restrictions pour faire partie de la communaut&eacute; Mecano'm.</p>
<p>Pour utiliser Mecano'm :</p>
<ul>
    <li>Vous devez avoir au moins 15 ans.</li>
    <li>Vous ne devez pas &ecirc;tre un d&eacute;linquant sexuel condamn&eacute;.</li>
    <li>Vous ne pouvez pas usurper l'identit&eacute; des autres ou fournir des informations inexactes.</li>
    <li>Vous devez nous fournir des informations exactes et &agrave; jour.</li>
    <li>Vous ne pouvez rien faire d'ill&eacute;gal, trompeur, frauduleux ou &agrave; des fins ill&eacute;gales ou non autoris&eacute;es.</li>
    <li>Vous ne pouvez pas enfreindre (ou aider ou encourager d'autres personnes &agrave; violer) ces Conditions ou nos r&egrave;gles. Vous devez nous signaler tout comportement suspect &agrave; travers contactez-nous.</li>
    <li>Vous ne pouvez rien faire qui puisse entraver ou entraver le fonctionnement pr&eacute;vu du Service.</li>
    <li>Vous ne pouvez pas essayer de cr&eacute;er des comptes ou d'acc&eacute;der ou de collecter des informations de mani&egrave;re non autoris&eacute;e.</li>
</ul>
<p>Dans le cadre de notre contrat, vous nous donnez &eacute;galement les autorisations n&eacute;cessaires pour fournir le service.</p>
<ul>
    <li>Autorisation d'utiliser votre nom d'utilisateur, votre photo de profil et des informations sur vos op&eacute;rations pour vous sugg&eacute;rer des annonces, des offres et tout autre contenu sponsoris&eacute; que vous pourrez suivre ou engager.</li>
    <li>Vous acceptez que nous puissions t&eacute;l&eacute;charger et installer les mises &agrave; jour du service sur votre appareil.</li>
    <li>Nous pouvons refuser de vous fournir ou d'arr&ecirc;ter de vous fournir tout ou partie du Service (y compris de r&eacute;silier ou de d&eacute;sactiver votre compte) imm&eacute;diatement pour prot&eacute;ger notre communaut&eacute; ou nos services, ou si vous cr&eacute;ez un risque ou une exposition l&eacute;gale pour nous, enfreignez les pr&eacute;sentes Conditions d'utilisation ou si nous sommes autoris&eacute;s ou oblig&eacute;s de le faire par la loi. Si nous prenons des mesures pour d&eacute;sactiver ou r&eacute;silier votre compte, nous vous en informerons le cas &eacute;ch&eacute;ant. Si vous pensez que votre compte a &eacute;t&eacute; ferm&eacute; par erreur, ou si vous souhaitez d&eacute;sactiver ou supprimer d&eacute;finitivement votre compte, contactez-nous directement.</li>
</ul>
<p> </p>
<ul>
    <li><strong><strong>Partage de responsabilit&eacute; et gestion de diff&eacute;rends</strong></strong></li>
</ul>
<p>Nous prenons toutes les mesures pour fournir un service s&eacute;curis&eacute; qui fonctionnera parfaitement tout le temps. Toutefois, nous ne sommes pas responsables de leurs (ou de vos) actions ou de leur comportement (en ligne ou hors ligne). Nous ne sommes &eacute;galement pas responsables des services et des fonctionnalit&eacute;s propos&eacute;s par d&rsquo;autres personnes ou compagnies, m&ecirc;me si vous y acc&eacute;dez via notre service.</p>
<p>Notre responsabilit&eacute; pour tout ce qui se passe sur le Service (&eacute;galement appel&eacute; &laquo;responsabilit&eacute;&raquo;) est limit&eacute;e autant que la loi le permet. S'il y a un probl&egrave;me avec notre service, nous ne pouvons pas savoir tous les impacts possibles. </p>
<p>Tout litige ou diff&eacute;rend sera g&eacute;r&eacute; &agrave; l&acute;amiable entre les parties concern&eacute;es. Dans le cas o&ugrave; une entente n&acute;est pas trouv&eacute;e, vous pouvez porter votre r&eacute;clamation aupr&egrave;s de tout tribunal comp&eacute;tent pour trouver la solution.</p>
<p></p>
<ul>
    <li><strong><strong>Autres conditions</strong></strong></li>
</ul>
<p>Nous appr&eacute;cions toujours les commentaires ou autres suggestions, mais nous pouvons les utiliser sans aucune restriction ni obligation de vous d&eacute;dommager, et nous n&rsquo;avons aucune obligation de les garder confidentielles.</p>
<p>  </p>
<ul>
    <li><strong><strong>Mise &agrave; jour de ces conditions</strong></strong></li>
</ul>
<p>Nous pouvons modifier nos services et nos r&egrave;gles, et il se peut que nous devions apporter des modifications &agrave; ces conditions pour qu'elles refl&egrave;tent fid&egrave;lement nos services et nos r&egrave;gles. Sauf disposition contraire de la loi, nous vous en informerons (par exemple, via notre Service) avant de modifier les pr&eacute;sentes Conditions et vous donnerons l'occasion de les r&eacute;viser avant leur entr&eacute;e en vigueur. Ensuite, si vous continuez &agrave; utiliser le service, vous serez li&eacute; par les conditions mises &agrave; jour. Si vous ne souhaitez pas accepter ces Conditions ou des Conditions mises &agrave; jour, vous pouvez toujours supprimer votre compte.</p>
<p>R&eacute;vis&eacute; le 23 juin 2019</p>
</body>
</html>