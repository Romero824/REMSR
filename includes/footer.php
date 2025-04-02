                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Add active class to current nav link
        $(document).ready(function() {
            var current = location.pathname;
            $('.nav-link').each(function() {
                var $this = $(this);
                if($this.attr('href') && current.indexOf($this.attr('href')) !== -1) {
                    $this.addClass('active');
                }
            });
        });
    </script>
</body>
</html> 