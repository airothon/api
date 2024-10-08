# Solana ağında cüzdan oluşturma
solana-keygen new --outfile ~/my-keypair.json

# Solana RPC Bağlantısı Kur
solana config set --url https://api.testnet.solana.com

# SPL Token Programını Yükle
spl-token create-token

# Token Hesabı Oluştur
spl-token create-account <token_id>
Oluşturulan hesap adresini .env dosyasına ekleyin.

# Token Mintlemek
spl-token mint <token_id> <token_count> <account_address>

# Her Müşteri için Token Hesabı Oluşturma
spl-token create-account <token_id> --owner <customer_address>
