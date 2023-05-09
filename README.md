# laravel-online-document
在线文档管理


### 软件架构
- Laravel:8.75
- PHP:7.3^8.0

### 示例
![API详情](./public/demo/api-detail.jpg)

### 功能列表
- [X] Auth
    - [X] 登录 /api/auth/login
    - [X] 登录会员信息 /api/auth/me
    - [X] 退出 /api/auth/logout
- [X] 项目内的权限验证
- [X] 项目管理
    - [X] 列表 /api/projects
    - [X] 详情 /api/project/detail
    - [X] 创建 /api/project/create
    - [X] 编辑 /api/project/update
    - [X] 删除 /api/project/delete
- [X] 分组管理
    - [X] 列表 /api/groups
    - [X] 创建 /api/group/create
    - [X] 编辑 /api/group/update
    - [X] 批量更新分组排序与归属父级 /api/group/batch-save
    - [X] 删除 /api/group/delete
    - [X] 是否默认打开子节点 /api/group/set-default-expand
- [X] API管理 
    - [X] 列表 /api/apis
    - [X] 详情 /api/api/detail
    - [X] 创建 /api/api/create
    - [X] 编辑 /api/api/update
    - [X] 删除 /api/api/delete
- [X] 文档管理 
    - [X] 列表 /api/docs
    - [X] 详情 /api/doc/detail
    - [X] 创建 /api/doc/create
    - [X] 编辑 /api/doc/update
    - [X] 删除 /api/doc/delete
- [X] 字段映射
    - [X] 列表 /api/field-mappings
    - [ ] ~~详情~~
    - [X] 创建 /api/field-mapping/create
    - [X] 编辑 /api/field-mapping/update
    - [X] 删除 /api/field-mapping/delete
- [X] 项目成员
    - [X] 列表 /api/project-members
      - 必须是项目创建人与管理员
    - [ ] ~~详情~~
    - [X] 创建 /api/project-member/create
      - 必须是项目创建人与管理员
    - [X] 编辑 /api/project-member/update
      - 必须是项目创建人与管理员
    - [X] 删除 /api/project-member/delete
      - 必须是项目创建人与管理员
    - [X] 设置权限 /api/project-member/set-role-power
      - 必须是项目创建人与管理员
    - [X] 设置成员的管理员权限 /api/project-member/set-leader
      - 必须是项目创建人
- [X] 操作日志
    - [X] 列表 /api/operation-logs
    - [X] 记录日志
      - [X] `项目`的新增、编辑、删除的日志
      - [X] `分组`的新增、编辑、删除的日志
      - [X] `API`的新增、编辑、删除的日志
      - [X] `文档`的新增、编辑、删除的日志
      - [X] `字段映射`的新增、编辑、删除的日志
      - [X] `成员`的新增、编辑、删除、设置权限、设置管理员的日志
