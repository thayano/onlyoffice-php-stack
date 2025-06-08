<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>App de Teste PHP - Editor na Página</title>
    <style>
        body { font-family: sans-serif; padding: 2em; }
        .editor-placeholder {
            width: 100%;
            height: 1000px;
            border: 1px solid #ccc;
            margin-top: 2em;
        }
        .file-link { color: #007bff; text-decoration: underline; cursor: pointer; }
        .file-link:hover { color: #0056b3; }
    </style>
</head>
<body>
    <h1>Pagina de Integração com ONLYOFFICE</h1>
    <p>Clique no arquivo para abri-lo no editor abaixo.</p>
    
    <ul id="file-list">
        <?php
            foreach (glob(__DIR__ . '/uploads/*') as $file) {
                $fileName = basename($file);
                echo '<li><a class="file-link" data-filename="' . htmlspecialchars($fileName) . '">' . htmlspecialchars($fileName) . '</a></li>';
            }
        ?>
    </ul>

    <div id="editor_placeholder"></div>

    <script type="text/javascript" src="http://localhost:8888/web-apps/apps/api/documents/api.js"></script>

    <script>
        // Variável global para manter a instância do editor
        let docEditor = null; 

        const fileLinks = document.querySelectorAll('.file-link');
        
        fileLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                
                const fileName = this.getAttribute('data-filename');
                const placeholder = document.getElementById('editor_placeholder');
                
                // Limpa o placeholder e destrói qualquer editor antigo antes de criar um novo
                if (docEditor) {
                    docEditor.destroyEditor();
                    docEditor = null;
                }
                placeholder.innerHTML = 'Carregando editor para ' + fileName + '...';
                placeholder.classList.add('editor-placeholder'); // Adiciona a classe para dar o estilo

                // Busca a configuração 
                fetch('api_config.php?file=' + encodeURIComponent(fileName))
                    .then(response => response.json())
                    .then(config => {
                        console.log( config);
                        if (config.error) {
                            placeholder.innerHTML = 'Erro ao carregar configuração: ' + config.error;
                            return;
                        }
                        
                        // Cria a nova instância do editor com a configuração recebida
                        docEditor = new DocsAPI.DocEditor('editor_placeholder', config);
                    })
                    .catch(error => {
                        placeholder.innerHTML = 'Ocorreu um erro de rede.';
                        console.error('Fetch Error:', error);
                    });
            });
        });
    </script>
</body>
</html>