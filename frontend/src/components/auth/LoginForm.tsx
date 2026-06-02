"use client";

import Image from "next/image";
import { useState, type FormEvent } from "react";
import { useRouter } from "next/navigation";
import { ROUTES } from "@/constants";
import { ErrorAlert, IconArrowRight, IconEye, IconLock, IconMail } from "@/components/shared";
import { Button as ShadcnButton } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Input as ShadcnInput } from "@/components/ui/input";
import { useLoginMutation } from "@/hooks/auth";
import { validateLogin } from "@/lib/auth/schemas";
import type { LoginCredentials } from "@/types";
import { Globe2, WebcamIcon } from "lucide-react";

export function LoginForm() {
  const router = useRouter();
  const loginMutation = useLoginMutation();
  const [values, setValues] = useState<LoginCredentials>({ email: "", password: "" });
  const [errors, setErrors] = useState<Partial<Record<keyof LoginCredentials, string>>>({});
  const [error, setError] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [rememberMe, setRememberMe] = useState(true);

  async function onSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const result = validateLogin(values);
    setErrors(result.errors);

    if (Object.values(result.errors).some(Boolean)) {
      return;
    }

    setError("");
    try {
      await loginMutation.mutateAsync(values);
      if (!rememberMe) {
        sessionStorage.setItem("inventory-session", "ephemeral");
      }
      router.replace(ROUTES.dashboard);
    } catch (loginError) {
      setError(loginError instanceof Error ? loginError.message : "We couldn't sign you in. Check your details and try again.");
    }
  }

  return (
    <main className="login-shell">
      <section className="login-hero-panel">
        <div className="login-brand">
          <Image src="/assets/logo-mark.svg" width={18} height={18} alt="" />
          <span>Inventory</span>
        </div>

        <div className="login-hero-copy">
          <p className="login-kicker">Admin dashboard</p>
          <h1>Every item, every supplier, accounted for.</h1>
          <p className="login-hero-text">
            Track stock levels, manage suppliers, and get alerted the moment something dips below threshold.
          </p>
        </div>

        <div className="login-orb login-orb-top" aria-hidden="true" />
        <div className="login-orb login-orb-bottom" aria-hidden="true" />
      </section>

      <section className="login-form-panel">
        <div className="login-card">
          <div className="login-card-head">
            <span className="login-mark">
              <Image src="/assets/logo-mark.svg" width={22} height={22} alt="" />
            </span>
            <h1>Grocery Inventory</h1>
            <p>Admin Dashboard · sign in to continue</p>
          </div>

          <form onSubmit={onSubmit} className="login-form-grid" noValidate>
            {error ? <ErrorAlert message={error} /> : null}

            <div className="login-field">
              <label htmlFor="email">Email</label>
              <div className={`login-input-wrap ${errors.email ? "invalid" : ""}`}>
                <IconMail size={16} />
                <ShadcnInput
                  id="email"
                  type="email"
                  autoComplete="email"
                  placeholder="alex@grocery.co"
                  value={values.email}
                  onChange={(event) => setValues((current) => ({ ...current, email: event.target.value }))}
                />
              </div>
              {errors.email ? <span className="login-field-error">{errors.email}</span> : null}
            </div>

            <div className="login-field">
              <div className="login-field-row">
                <label htmlFor="password">Password</label>
                <ShadcnButton type="button" variant="link" className="login-link-btn">Forgot password?</ShadcnButton>
              </div>
              <div className={`login-input-wrap ${errors.password ? "invalid" : ""}`}>
                <IconLock size={16} />
                <ShadcnInput
                  id="password"
                  type={showPassword ? "text" : "password"}
                  autoComplete="current-password"
                  placeholder="........"
                  value={values.password}
                  onChange={(event) => setValues((current) => ({ ...current, password: event.target.value }))}
                />
                <ShadcnButton
                  type="button"
                  variant="ghost"
                  size="icon-sm"
                  className="login-visibility-btn"
                  aria-label={showPassword ? "Hide password" : "Show password"}
                  onClick={() => setShowPassword((current) => !current)}
                >
                  <IconEye size={16} />
                </ShadcnButton>
              </div>
              {errors.password ? <span className="login-field-error">{errors.password}</span> : null}
            </div>

            <div className="login-check">
              <Checkbox id="remember-me" checked={rememberMe} onCheckedChange={() => setRememberMe((current) => !current)} />
              <label htmlFor="remember-me">Keep me signed in</label>
            </div>

            <ShadcnButton type="submit" className="login-submit" disabled={loginMutation.isPending}>
              <span>{loginMutation.isPending ? "Signing in..." : "Sign in"}</span>
              <IconArrowRight size={16} />
            </ShadcnButton>
          </form>

          <p className="login-footnote">
            Need an admin account? <br /> <div className="flex items-center gap-2 justify-center "> <p className="login-footnote-text">Contact the developer at:</p>  <a href="https://hamzahamir.site" target="_blank" rel="noopener noreferrer"><Globe2 size={16} /></a>  </div>
          </p>
        </div>
      </section>
    </main>
  );
}
