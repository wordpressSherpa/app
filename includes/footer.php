<script>
// Main Menu 
const menuBtn =
    document.getElementById('menuToggle');

const sidebar =
    document.getElementById('sidebar');

const content =
    document.getElementById('content');

menuBtn.addEventListener('click', () => {

    sidebar.classList.toggle('collapsed');
    content.classList.toggle('expanded');

});

</script>

</body>

</html>