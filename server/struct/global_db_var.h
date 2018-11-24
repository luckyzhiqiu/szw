//全局数据库全局变量结构体（对应global_var表）

//全局用户信息
struct UserInfo
{
	int32 allocUserID:1=10001;//分配的用户ID
}

//全局联盟信息
struct UnionInfo
{
	int32 allocUnionID:1=10001;//分配的联盟ID
}

//全局变量定义（全部成员会导入global_var表）
struct Var
{
	UserInfo userInfo:1;//全局用户信息
	UnionInfo unionInfo:2;//全局联盟信息
}