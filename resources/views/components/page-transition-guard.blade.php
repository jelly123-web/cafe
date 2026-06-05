<script>
  (function () {
    document.documentElement.classList.add('app-js', 'app-hydrating');
  })();
</script>
<style>
  html.app-js.app-hydrating body {
    opacity: 0;
  }

  html.app-js body {
    transition: opacity 160ms ease, filter 160ms ease;
  }

  html.app-js:not(.app-hydrating) body {
    opacity: 1;
  }

  html.app-navigating body {
    cursor: progress;
  }

  html.app-navigating .main-content,
  html.app-navigating .main-panel,
  html.app-navigating .dashboard-shell,
  html.app-navigating main.shell {
    opacity: 0.62;
    filter: saturate(0.96) blur(0.2px);
    pointer-events: none;
    transition: opacity 160ms ease, filter 160ms ease;
  }

  html.app-js .main-content,
  html.app-js .main-panel,
  html.app-js .dashboard-shell,
  html.app-js main.shell {
    animation: appPageIn 180ms ease both;
  }

  @keyframes appPageIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
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
