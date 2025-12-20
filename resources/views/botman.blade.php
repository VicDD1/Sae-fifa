<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
</head>
<body>
    <script>
        var botmanWidget = {
            frameEndpoint: '/botman/chat',
            chatServer: '/botman',
            title: 'Assistant Fifa',
            mainColor: '#0056b3',
            bubbleBackground: '#0056b3',
            userId: '{{ Auth::id() ?? "guest" }}',
            
            introMessage: "Bonjour ! Je suis l'assistant Fifa. Tapez aide pour voir ce que je peux faire sur cette page.",
            placeholderText: 'Posez votre question...',
            displayMessageTime: true,
        };

        function fixLinks() {
            var chatIframe = document.querySelector('#botmanWidgetRoot iframe');
            if (chatIframe) {
                var innerDoc = chatIframe.contentDocument || chatIframe.contentWindow.document;
                var links = innerDoc.querySelectorAll('a');
                links.forEach(function(link) {
                    if (link.getAttribute('target') !== '_parent') {
                        link.setAttribute('target', '_parent');
                    }
                });
            }
        }
        setInterval(fixLinks, 500);
    </script>
    
    <script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>
</body>
</html>