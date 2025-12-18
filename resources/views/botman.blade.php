<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon BotMan</title>
    <style>
        /* On s'assure juste que le widget ne crée pas de conflits visuels */
        #botmanWidgetRoot {
            position: fixed;
            z-index: 9999;
        }
    </style>
</head>
<body>

    <script>
        var botmanWidget = {
            frameEndpoint: '/botman/chat', // L'URL qui charge la logique de chat
            chatServer: '/botman',      // L'URL de handle() dans votre contrôleur
            title: 'Assistant',
            mainColor: '#4080FF',
            bubbleBackground: '#4080FF',
            bubbleAvatarUrl: '',
            aboutText: 'Service Client',
            introMessage: "Bonjour ! Comment puis-je vous aider ? (Tapez 'aide' pour voir les options)",
            placeholderText: 'Écrivez votre message...',
            displayMessageTime: true,
        };
    </script>

    <script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>

</body>
</html>