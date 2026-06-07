<!-- mt-auto dans flex-col pousse le footer vers le bas même si le contenu est court -->
        <footer class="bg-[#071d3b]/70 text-white/60 w-auto mt-auto backdrop-blur-sm">
            <div class="max-w-6xl mx-auto p-6 text-center text-sm">
                <!-- <?= date('Y') ?> affiche l'année courante dynamiquement -->
                <!-- Correction du bug original : <?php date('Y') ?> n'affichait rien -->
                <p>© <?= date('Y') ?> Mpandova — Orientation académique à Madagascar</p>
            </div>
        </footer>

    </body>
</html>