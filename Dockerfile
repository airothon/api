FROM solanalabs/solana:v1.7.11

RUN apt-get update && apt-get install -y \
    build-essential \
    pkg-config \
    libudev-dev \
    libssl-dev \
    && cargo install spl-token-cli
