set :application, 'envisioningjustice_staging'
set :login, 'ilhumanities'
set :repo_url, 'git@github.com:firebelly/envisioning-justice.git'
set :theme, 'envisioningjustice'
set :domain, 'staging.envisioningjustice.org'

# For wpcli db command search-replace
set :wpcli_remote_url, "https://#{fetch(:domain)}"
set :wpcli_local_url, "http://envisioning-justice.localhost"

# This can be overridden in a stage config file to pull from a different branch
set :branch, :master

set :deploy_to, -> { "/home/#{fetch(:login)}/webapps/#{fetch(:application)}" }

set :tmp_dir, -> { "/home/#{fetch(:login)}/tmp" }

# Use :debug for more verbose output when troubleshooting
set :log_level, :info

# Apache users with .htaccess files:
# it needs to be added to linked_files so it persists across deploys:
set :linked_files, fetch(:linked_files, []).push('.env', 'web/.htaccess', 'web/php.ini')
set :linked_dirs, fetch(:linked_dirs, []).push('web/app/uploads')

namespace :deploy do
  desc 'Restart application'
  task :restart do
    on roles(:app), in: :sequence, wait: 5 do
      # Your restart mechanism here, for example:
      # execute :service, :nginx, :reload
      # capture("#{deploy_to}/bin/restart")
    end
  end
end

# The above restart task is not run by default
# Uncomment the following line to run it on deploys if needed
# after 'deploy:publishing', 'deploy:restart'

namespace :deploy do
  desc 'Update WordPress template root paths to point to the new release'
  task :update_option_paths do
    on roles(:app) do
      within fetch(:release_path) do
        if test :wp, :core, 'is-installed'
          [:stylesheet_root, :template_root].each do |option|
            # Only change the value if it's an absolute path
            # i.e. The relative path "/themes" must remain unchanged
            # Also, the option might not be set, in which case we leave it like that
            value = capture :wp, :option, :get, option, raise_on_non_zero_exit: false
            if value != '' && value != '/themes'
              execute :wp, :option, :set, option, fetch(:release_path).join('web/wp/wp-content/themes')
            end
          end
        end
      end
    end
  end
end

# The above update_option_paths task is not run by default
# Note that you need to have WP-CLI installed on your server
# Uncomment the following line to run it on deploys if needed
# after 'deploy:publishing', 'deploy:update_option_paths'



# GULP! compile production assets and copy to server, then UNGULP! to dev mode
# borrowing from https://gist.github.com/christhesoul/3c38053971a7b786eff2 & https://gist.github.com/nateroling/22b51c0cfbe210b00698

set :theme_path, Pathname.new('web/app/themes/').join(fetch(:theme))
set :local_app_path, Pathname.new(File.dirname(__FILE__)).join('../')
set :local_theme_path, fetch(:local_app_path).join(fetch(:theme_path))

# Set path to composer
namespace :deploy do
  before :starting, :map_composer_command do
      on roles(:app) do |server|
          SSHKit.config.command_map[:composer] = "php72 /home/#{fetch(:login)}/bin/composer.phar"
      end
  end
end

namespace :deploy do
  task :compile_assets do
    run_locally do
      execute "cd #{fetch(:local_theme_path)} && ./node_modules/.bin/gulp --production"
    end
  end

  task :copy_assets do
    invoke 'deploy:compile_assets'

    on roles(:web) do
      upload! fetch(:local_theme_path).join('dist').to_s, release_path.join(fetch(:theme_path)), recursive: true
    end
  end
end

before "deploy:updated", "deploy:copy_assets"
