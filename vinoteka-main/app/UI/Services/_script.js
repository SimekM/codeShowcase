
<script>
    const images = document.querySelectorAll('section.gallery .photo-grid img');

    images.forEach((image) => {
        image.addEventListener('click', () => {
            const modal = document.createElement('div');
            modal.classList.add('modal');
            modal.innerHTML = `
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <img src="${ image.src }" class="modal-image">
                </div>
            `;

            const close = modal.querySelector('.close');
            close.addEventListener('click', () => {
                modal.remove();
            });

            document.body.appendChild(modal);

            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        });
    });
</script>