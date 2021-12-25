clean:
	rm -rf build

build:
	mkdir build
	ppm --compile="src/TelegramClientManager" --directory="build"

update:
	ppm --generate-package="src/TelegramClientManager"

install:
	ppm --no-prompt --install="build/net.intellivoid.telegram_client_manager.ppm" --fix-conflict

install_fast:
	ppm --no-prompt --install="build/net.intellivoid.telegram_client_manager.ppm" --fix-conflict --skip-dependencies