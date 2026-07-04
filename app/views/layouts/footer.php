<!-- mt-auto dans flex-col pousse le footer vers le bas même si le contenu est court -->
        <footer class="border-b border-white/15 bg-[#071d3b]/80 text-white backdrop-blur-md">
            <div class="max-w-6xl mx-auto p-6 text-center text-sm">
                <!-- <?= date('Y') ?> affiche l'année courante dynamiquement -->
                <!-- Correction du bug original : <?php date('Y') ?> n'affichait rien -->
                <p>© <?= date('Y') ?> Mpandova | Orientation académique à Madagascar</p>
            </div>
        </footer>

    </body>
</html>