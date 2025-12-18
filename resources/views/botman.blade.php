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
            params: {
                current_url: window.location.pathname 
            },
            introMessage: "{{ Auth::check() ? 'Bonjour ' . Auth::user()->name . ' ! Comment puis-je vous aider ?' : 'Bonjour ! Tapez aide pour voir les liens de navigation.' }}",
            placeholderText: 'Posez votre question...'
        };

        function fixLinks() {
            var chatIframe = document.querySelector('#botmanWidgetRoot iframe');
            if (chatIframe) {
                var innerDoc = chatIframe.contentDocument || chatIframe.contentWindow.document;
                var links = innerDoc.querySelectorAll('a');
                links.forEach(function(link) {
                    link.setAttribute('target', '_parent');
                    link.style.color = "#0056b3";
                    link.style.fontWeight = "bold";
                    link.style.textDecoration = "underline";
                });
            }
        }
        setInterval(fixLinks, 400);
    </script>
    <script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>
</body>
</html>