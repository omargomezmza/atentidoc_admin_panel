<script>
    setInterval(() => {
        fetch('/refresh-csrf')
            .then(r => r.json())
            .then(d => {
                console.log('refresh');
                document.querySelectorAll('input[name="_token"]').forEach(el => {
                    el.value = d.token;
                    console.log('refresh OK');
                });
            });
    }, 5 * 60 * 1000); // cada 5 minutos
</script>
