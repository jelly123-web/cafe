<script>
  (function () {
    document.documentElement.classList.add('app-js', 'app-hydrating');
  })();
</script>
<style>
  html,
  body {
    overflow-x: hidden;
  }

  html.app-js body {
    opacity: 1;
  }

  html.app-navigating body {
    cursor: progress;
  }

  html.app-js .main-content,
  html.app-js .main-panel,
  html.app-js .dashboard-shell,
  html.app-js main.shell {
    opacity: 1;
    filter: none;
    transform: none;
  }

  @media (prefers-reduced-motion: reduce) {
    html.app-js body,
    html.app-js .main-content,
    html.app-js .main-panel,
    html.app-js .dashboard-shell,
    html.app-js main.shell {
      transition: none !important;
      animation: none !important;
      transform: none !important;
    }
  }
</style>
<script>
  (function () {
    const root = document.documentElement;
    const settle = function () {
      requestAnimationFrame(function () {
        root.classList.remove('app-hydrating', 'app-navigating');
      });
    };

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', settle, { once: true });
    } else {
      settle();
    }

    document.addEventListener('turbo:before-visit', function () {
      root.classList.add('app-navigating');
    });

    document.addEventListener('turbo:before-render', function () {
      root.classList.add('app-hydrating');
    });

    document.addEventListener('turbo:render', settle);
    document.addEventListener('turbo:load', settle);
    window.addEventListener('pageshow', settle);

    setTimeout(settle, 1200);
  })();
</script>
