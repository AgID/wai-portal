#!/bin/sh

find -P /tmp/premium_plugins/* -prune -type d -printf "%f\n" | while IFS= read -r directory; do
    # Extract plugin code
    unzip -u "/tmp/premium_plugins/$directory/*.zip" -d /opt/matomo/plugins

    if ! grep -q "$directory" /opt/matomo/config/config.ini.php; then
        # Add plugin into "installed" list
        sed -i -E -e "/^PluginsInstalled\[\] = .*$/!b;:a;n;//ba;i\PluginsInstalled\[\] = \"$directory\"" /opt/matomo/config/config.ini.php

        # Add plugin into "activated" list
        sed -i -E -e "/^Plugins\[\] = .*$/!b;:a;n;//ba;i\Plugins\[\] = \"$directory\"" /opt/matomo/config/config.ini.php

        # Add plugin specific configuration
        if [ -f "/tmp/premium_plugins/$directory/config.ini.php" ]; then
            echo "[$directory]"  >> /opt/matomo/config/config.ini.php
            cat "/tmp/premium_plugins/$directory/config.ini.php"  >> /opt/matomo/config/config.ini.php
            echo ""  >> /opt/matomo/config/config.ini.php
        fi
    fi
done
