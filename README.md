# aiit-cloud-infra

## ディレクトリ
- app: ソースコードが入っています
- docs: 環境構築時のメモが入っています
- images: docker image が入っています

## コードの大体の場所
基本的に Laravel の規則に沿っています。

### DataCenterManager
app/app/Jobs/DataCenterManager

### Agent
app/app/Jobs/Agent

### Web
#### Routing
- app/routes/api.php
- app/routes/web.php

#### Controller
app/app/Http/Controllers

#### Model
app/app/Models

#### View
app/resources/views

### インスタンスのライフサイクルの管理
app/app/Services/InstanceManager.php

### Dockerコンテナの管理
app/app/Services/DockerContainerManager.php

## API Documentの出力方法
APIドキュメントは以下のコマンドで生成します。

```shell
cd app
fish generate_api_doc.fish
```

このコマンドの実行には以下が必要です。

- fish shell
- git
- docker

実行後にブラウザで `http://{domain}/api/1.0/doc` にアクセスすると見れます。
