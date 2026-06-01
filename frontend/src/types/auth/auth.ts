export type LoginPayload = {
  email: string;
  password: string;
};

export type LoginCredentials = LoginPayload;

export type AuthUser = {
  id: number;
  name: string;
  email: string;
};

export type AuthPayload = {
  token: string;
  user: AuthUser;
};

export type LoginResponse = AuthPayload;
