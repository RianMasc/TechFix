</div> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Se o arquivo atual estiver dentro da pasta "pages", volta um nível, senão vai na fé
$caminhoJs = (basename(dirname($_SERVER['PHP_SELF'])) === 'pages') ? '../assets/js/script.js' : 'assets/js/script.js';
?>
<script src="<?= $caminhoJs; ?>?v=<?= time(); ?>"></script>
</body>
</html>