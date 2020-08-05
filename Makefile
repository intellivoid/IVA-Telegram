clean:
	rm -rf build

build:
	mkdir build
	ppm --compile="src/TelegramClientManager" --directory="build"

install:
	ppm --no-prompt --install="build/net.intellivoid.telegram_client_manager.ppm"