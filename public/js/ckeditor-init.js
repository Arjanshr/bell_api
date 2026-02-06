window.initCkeditor = (selector = '.ckeditor') => {
    const tiktokProvider = {
        name: 'tiktok',
        url: /^https:\/\/www\.tiktok\.com\/@[\w.-]+\/video\/(\d+)/,
        html: function (match) {
            const videoId = match[1];
            const url = match[0];
            return (
                '<blockquote class="tiktok-embed" cite="' + url + '" data-video-id="' + videoId +
                '" style="max-width: 605px;min-width: 325px;">' +
                '<section>Loading...</section>' +
                '</blockquote>' +
                '<script async src="https://www.tiktok.com/embed.js"><\/script>'
            );
        }
    };

    document.querySelectorAll(selector).forEach(editorEl => {
        // Prevent re-initialization
        if (editorEl.getAttribute('data-ckeditor-initialized')) return;

        ClassicEditor
            .create(editorEl, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                ckfinder: {
                    uploadUrl: editorEl.dataset.uploadUrl || '',
                },
                mediaEmbed: {
                    extraProviders: [tiktokProvider]
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                        { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' }
                    ]
                }
            })
            .then(editor => {
                editorEl.setAttribute('data-ckeditor-initialized', 'true');

                // Create a stats container only once
                let statsDiv = document.createElement('div');
                statsDiv.classList.add('text-muted', 'mt-1');
                statsDiv.style.fontSize = '0.875em';
                statsDiv.innerText = 'Characters: 0 | Words: 0';

                // Insert after editorEl (textarea)
                editorEl.parentNode.insertBefore(statsDiv, editorEl.nextSibling);

                const updateStats = () => {
                    const text = editor.getData().replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
                    const charCount = text.length;
                    const wordCount = text === '' ? 0 : text.split(/\s+/).length;
                    statsDiv.innerText = `Characters: ${charCount} | Words: ${wordCount}`;
                };

                editor.model.document.on('change:data', updateStats);
                updateStats(); // initial
            })
            .catch(error => {
                console.error(error);
            });
    });
};
